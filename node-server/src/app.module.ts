import { Module } from '@nestjs/common';
import { ConfigModule, ConfigService } from '@nestjs/config';
import { TypeOrmModule } from '@nestjs/typeorm';
import { AppController } from './app.controller';
import { AppService } from './app.service';
import { getOrmConfig } from './configs/orm.config';
import { RegionModule } from './region/region.module';
import { BboxModule } from './bbox/bbox.module';
import { UserModule } from './user/user.module';
import { StreetModule } from './street/street.module';
import { SegmentModule } from './segment/segment.module';
import { ConnectionModule } from './connection/connection.module';
import { RoadTypeModule } from './roadType/roadType.module';

@Module({
  imports: [
    TypeOrmModule.forRootAsync({
      imports: [ConfigModule],
      inject: [ConfigService],
      useFactory: getOrmConfig,
    }),
    ConfigModule.forRoot(),
    BboxModule,
    ConnectionModule,
    RegionModule,
    RoadTypeModule,
    SegmentModule,
    StreetModule,
    UserModule,
  ],
  controllers: [AppController],
  providers: [AppService],
})
export class AppModule {}
